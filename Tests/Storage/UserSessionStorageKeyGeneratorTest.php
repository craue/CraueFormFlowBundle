<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Exception\InvalidTypeException;
use Craue\FormFlowBundle\Storage\StorageKeyGeneratorInterface;
use Craue\FormFlowBundle\Storage\UserSessionStorageKeyGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2024 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class UserSessionStorageKeyGeneratorTest extends TestCase {

	/**
	 * @var StorageKeyGeneratorInterface
	 */
	protected $generator;

	/**
	 * @var TokenStorageInterface
	 */
	protected $tokenStorage;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp() : void {
		$session = new Session(new MockArraySessionStorage());
		$session->setId('12345');
		$this->tokenStorage = new TokenStorage();
		$this->generator = $this->createGenerator($session);
	}

	private function createGenerator(SessionInterface $session) : UserSessionStorageKeyGenerator {
		$requestStackMock = $this->getMockBuilder(RequestStack::class)->onlyMethods(['getSession'])->getMock();

		$requestStackMock
			->method('getSession')
			->willReturn($session)
		;

		return new UserSessionStorageKeyGenerator($this->tokenStorage, $requestStackMock);
	}

	/**
	 * @dataProvider dataGenerate_mockedTokens
	 */
	public function testGenerate_mockedTokens($expectedKey, $username) {
		$token = $this->getMockBuilder(AbstractToken::class)->onlyMethods(['getUserIdentifier'])->getMockForAbstractClass();

		$token
			->expects($this->once())
			->method('getUserIdentifier')
			->will($this->returnValue($username))
		;

		$this->tokenStorage->setToken($token);
		$this->assertSame($expectedKey, $this->generator->generate('key'));
	}

	public function dataGenerate_mockedTokens() {
		yield ['session_12345_key', ''];
		yield ['user_username_key', 'username'];
	}

	/**
	 * @dataProvider dataGenerate_realTokens
	 */
	public function testGenerate_realTokens($expectedKey, $token) {
		$this->tokenStorage->setToken($token);
		$this->assertSame($expectedKey, $this->generator->generate('key'));
	}

	public function dataGenerate_realTokens() : iterable {
		yield ['session_12345_key', null];

		yield ['user_username_key', new PreAuthenticatedToken(new InMemoryUser('username', 'password'), 'firewall')];

		if (Kernel::VERSION_ID < 70200) {
			// TODO remove as soon as Symfony >= 7.2 is required
			yield ['user_username_key', new RememberMeToken(new InMemoryUser('username', 'password'), 'firewall', 'secret')];
		} else {
			yield ['user_username_key', new RememberMeToken(new InMemoryUser('username', 'password'), 'firewall')];
		}

		yield ['user_username_key', new UsernamePasswordToken(new InMemoryUser('username', 'password'), 'firewall')];
	}

	public function testGenerate_invalidArgument() {
		$this->expectException(InvalidTypeException::class);
		$this->expectExceptionMessage('Expected argument of type "string", but "NULL" given.');

		$this->generator->generate(null);
	}

	public function testGenerate_emptyArgument() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Argument must not be empty.');

		$this->generator->generate('');
	}

}
