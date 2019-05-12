<?php
namespace ProfiCloS\SmsGateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Nette\Utils\Strings;

class Gateway
{

	public const MODE_DEVELOPMENT = 'dev';
	public const MODE_PRODUCTION = 'prod';

	protected const ACTION_SEND_SMS = 'send_sms';

	protected $url = 'https://api.smsbrana.cz/smsconnect/http.php';
	private $login;
	private $password;
	public $mode = self::MODE_PRODUCTION;

	/**
	 * @param $login
	 * @param $password
	 */
	public function __construct(string $login, string $password)
	{
		$this->login = $login;
		$this->password = $password;
	}

	/**
	 * @param $mode
	 * @return Gateway
	 */
	public function setMode($mode): self
	{
		$this->mode = $mode;
		return $this;
	}

	/**
	 * Send SMS
	 * If is mode setted to Development, return true
	 * @param string $phoneNumber
	 * @param string $text
	 * @return bool
	 * @throws Exception
	 */
	public function send(string $phoneNumber, string $text): ?bool
	{
		$text = Strings::toAscii($text);
		$phoneNumber = str_replace(' ', '', $phoneNumber);

		if ($this->mode === self::MODE_DEVELOPMENT) {
			return TRUE;
		}

		try {

			$guzzleClient = new Client();
			$response = $guzzleClient->get($this->url, [
				'query' => [
					'login' => $this->login,
					'password' => $this->password,
					'action' => self::ACTION_SEND_SMS,
					'number' => $phoneNumber,
					'message' => $text
				]
			]);
			$responseContent = $response->getBody()->getContents();

			$response = @simplexml_load_string($responseContent);
			switch ((string)$response->err) {
				case '0':
					return TRUE;
				case '1':
					throw new RuntimeException('Undefined error');
				case '2':
					throw new AuthenticationException('Bad login');
				case '3':
					throw new AuthenticationException('Bad hash or password');
				case '4':
					throw new AuthenticationException('Bad timestamp');
				case '5':
					throw new AuthenticationException('Unallowed IP');
				case '6':
					throw new ServerException('Undefined action');
				case '7':
					throw new AuthenticationException('This salt already used this day');
				case '8':
					throw new ServerException('Database connection error');
				case '9':
					throw new CreditException('');
				case '10':
					return FALSE;
				case '11':
					return FALSE;
				case '12':
					return FALSE;
				default:
					throw new Exception('Not recognized error');
			}

		} catch (ClientException $e) {
			throw new ServerException('Gateway is not available');
		}
	}

}
