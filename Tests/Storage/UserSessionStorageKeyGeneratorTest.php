<?php

namespace Craue\FormFlowBundle\Tests\Storage;

use Craue\FormFlowBundle\Storage\StorageKeyGeneratorInterface;
use Craue\FormFlowBundle\Storage\UserSessionStorageKeyGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\User;

/**
 * @group unit
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2020 Christian Raue
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
	protected function setUp() {
		$session = new Session(new MockArraySessionStorage());
		$session->setId('12345');
		$this->tokenStorage = new TokenStorage();
		$this->generator = new UserSessionStorageKeyGenerator($this->tokenStorage, $session);
	}

	/**
	 * @dataProvider dataGenerate_mockedTokens
	 */
	public function testGenerate_mockedTokens($expectedKey, $username) {
		$token = $this->getMockBuilder(AbstractToken::class)->setMethods(['getUsername'])->getMockForAbstractClass();

		$token
			->expects($this->once())
			->method('getUsername')
			->will($this->returnValue($username))
		;

		$this->tokenStorage->setToken($token);
		$this->assertSame($expectedKey, $this->generator->generate('key'));
	}

	public function dataGenerate_mockedTokens() {
		return [
			['session_12345_key', null],
			['session_12345_key', ''],
			['user_username_key', 'username'],
		];
	}

	/**
	 * @dataProvider dataGenerate_realTokens
	 */
	public function testGenerate_realTokens($expectedKey, $token) {
		$this->tokenStorage->setToken($token);
		$this->assertSame($expectedKey, $this->generator->generate('key'));
	}

	public function dataGenerate_realTokens() {
		return [
			['session_12345_key', null],
			['session_12345_key', new AnonymousToken('secret', '')],
			['session_12345_key', new AnonymousToken('secret', 'username')],
			['session_12345_key', new PreAuthenticatedToken('', 'password', 'firewall')],
			['user_username_key', new PreAuthenticatedToken('username', 'password', 'firewall')],
			['user_username_key', new RememberMeToken(new User('username', 'password'), 'firewall', 'secret')],
			['session_12345_key', new UsernamePasswordToken('', 'password', 'firewall')],
			['user_username_key', new UsernamePasswordToken('username', 'password', 'firewall')],
			['user_username_key', new UsernamePasswordToken(new User('username', 'password'), 'password', 'firewall')],
		];
	}

	/**
	 * @expectedException \Craue\FormFlowBundle\Exception\InvalidTypeException
	 * @expectedExceptionMessage Expected argument of type "string", but "NULL" given.
	 */
	public function testGenerate_invalidArgument() {
		$this->generator->generate(null);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Argument must not be empty.
	 */
	public function testGenerate_emptyArgument() {
		$this->generator->generate('');
	}

}
