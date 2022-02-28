<?php

/**
 * PHPMailer - PHP email transport unit tests.
 * PHP version 5.5.
 *
 * @author    Marcus Bointon <phpmailer@synchromedia.co.uk>
 * @author    Andy Prevost
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace PHPMailer\Test\OAuth;

use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\OAuthTokenProvider;
use PHPMailer\PHPMailer\PHPMailer;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test OAuth functionality.
 */
final class OAuthTest extends TestCase
{
    /**
     * Test OAuth class.
     *
     * @covers PHPMailer\PHPMailer\PHPMailer::getOAuth
     * @covers PHPMailer\PHPMailer\PHPMailer::setOAuth
     * @covers PHPMailer\PHPMailer\OAuth::__construct
     * @covers PHPMailer\PHPMailer\OAuthTokenProvider
     */
    public function testOAuth()
    {
        $PHPMailer = new PHPMailer(true);
        $reflection = new \ReflectionClass($PHPMailer);
        $property = $reflection->getProperty('oauth');
        $property->setAccessible(true);
        $property->setValue($PHPMailer, true);
        self::assertTrue($PHPMailer->getOAuth(), 'Initial value of oauth property is not true');

        $options = [
            'provider' => 'dummyprovider',
            'userName' => 'dummyusername',
            'clientSecret' => 'dummyclientsecret',
            'clientId' => 'dummyclientid',
            'refreshToken' => 'dummyrefreshtoken',
        ];

        $oauth = new OAuth($options);
        self::assertInstanceOf(OAuth::class, $oauth, 'Instantiation of OAuth class failed');
        self::assertInstanceOf(OAuthTokenProvider::class, $oauth, 'Instantiation of OAuth class failed');
        $subject = $PHPMailer->setOAuth($oauth);
        self::assertNull($subject, 'setOAuth() is not a void function');
        self::assertInstanceOf(
            OAuth::class,
            $PHPMailer->getOAuth(),
            'Setting Oauth property to an instance of the OAuth class failed'
        );
        $this->expectException(\Error::class);
        $PHPMailer->setOAuth(new \stdClass());
        $PHPMailer->setOAuth(new DummyOAuthProvider());
    }

    /**
     * Test OAuth class on more recent PHP versions.
     *
     * @requires PHP >= 7.0
     * @covers PHPMailer\PHPMailer\PHPMailer::setOAuth
     * @covers PHPMailer\PHPMailer\OAuthTokenProvider
     */
    public function testOAuthRecent()
    {
        $PHPMailer = new PHPMailer(true);

        $this->expectException(\TypeError::class);
        $PHPMailer->setOAuth(new \stdClass());
    }
}

class DummyOauthProvider implements OAuthTokenProvider
{
    public function getOauth64()
    {
        return 'oauth';
    }
}
