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
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\User;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2021 Christian Raue
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
		// TODO remove as soon as Symfony >= 5.3 is required
		if (!\method_exists(RequestStack::class, 'getSession')) {
			return new UserSessionStorageKeyGenerator($this->tokenStorage, $session);
		}

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
		// TODO just use 'getUserIdentifier' as soon as Symfony >= 5.3 is required
		$methodName = \method_exists(AbstractToken::class, 'getUserIdentifier') ? 'getUserIdentifier' : 'getUsername';

		$token = $this->getMockBuilder(AbstractToken::class)->onlyMethods([$methodName])->getMockForAbstractClass();

		$token
			->expects($this->once())
			->method($methodName)
			->will($this->returnValue($username))
		;

		$this->tokenStorage->setToken($token);
		$this->assertSame($expectedKey, $this->generator->generate('key'));
	}

	public function dataGenerate_mockedTokens() {
		// TODO remove as soon as Symfony >= 5.3 is required
		if (!\method_exists(AbstractToken::class, 'getUserIdentifier')) {
			yield ['session_12345_key', null];
		}

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
		// TODO just use `InMemoryUser` as soon as Symfony >= 5.3 is required
		$userClass = \class_exists(InMemoryUser::class) ? InMemoryUser::class : User::class;

		yield ['session_12345_key', null];

		if (Kernel::VERSION_ID < 50400) {
			// TODO remove as soon as Symfony >= 5.4 is required
			yield ['session_12345_key', new AnonymousToken('secret', '')];
			yield ['session_12345_key', new AnonymousToken('secret', 'username')];
		}

		if (Kernel::VERSION_ID < 50400) {
			// TODO remove as soon as Symfony >= 5.4 is required
			yield ['session_12345_key', new PreAuthenticatedToken('', 'password', 'firewall')];
			yield ['user_username_key', new PreAuthenticatedToken('username', 'password', 'firewall')];
		} else {
			yield ['user_username_key', new PreAuthenticatedToken(new $userClass('username', 'password'), 'firewall')];
		}

		yield ['user_username_key', new RememberMeToken(new $userClass('username', 'password'), 'firewall', 'secret')];

		if (Kernel::VERSION_ID < 50400) {
			// TODO remove as soon as Symfony >= 5.4 is required
			yield ['session_12345_key', new UsernamePasswordToken('', 'password', 'firewall')];
			yield ['user_username_key', new UsernamePasswordToken('username', 'password', 'firewall')];
			yield ['user_username_key', new UsernamePasswordToken(new $userClass('username', 'password'), 'password', 'firewall')];
		} else {
			yield ['user_username_key', new UsernamePasswordToken(new $userClass('username', 'password'), 'firewall')];
		}
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
