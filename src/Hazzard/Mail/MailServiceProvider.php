<?php namespace Hazzard\Mail;

use Swift_Mailer;
use Hazzard\Support\ServiceProvider;
use Swift_SmtpTransport as SmtpTransport;
use Swift_MailTransport as MailTransport;
use Swift_SendmailTransport as SendmailTransport;

class MailServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$me = $this;

		$this->app->bindShared('mailer', function($app) use($me) {
			$me->registerSwiftMailer();

			$mailer = new Mailer($app['view'], $app['swift.mailer'], $app['events']);

			$from = $app['config']['mail.from'];

			if (is_array($from) && isset($from['address'])) {
				$mailer->alwaysFrom($from['address'], $from['name']);
			}

			return $mailer;
		});
	}

	/**
	 * Register the Swift Mailer instance.
	 *
	 * @return void
	 */
	public function registerSwiftMailer()
	{
		$config = $this->app['config']['mail'];

		$this->registerSwiftTransport($config);

		$this->app['swift.mailer'] = new Swift_Mailer($this->app['swift.transport']);
	}

	/**
	 * Register the Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function registerSwiftTransport($config)
	{
		switch ($config['driver']) {
			case 'smtp':
				extract($config);

				$transport = SmtpTransport::newInstance($host, $port);

				if (isset($encryption)) {
					$transport->setEncryption($encryption);
				}

				if (isset($username)) {
					$transport->setUsername($username);
					$transport->setPassword($password);
				}

				$this->app['swift.transport'] = $transport;
			break;

			case 'sendmail':
				$this->app['swift.transport'] = SendmailTransport::newInstance($config['sendmail']);
			break;

			case 'mail':
				$this->app['swift.transport'] = MailTransport::newInstance();
			break;

			case 'mailgun':
				$mailgun = $this->app['config']['services.mailgun'];
				$this->app['swift.transport'] = new MailgunTransport($mailgun['secret'], $mailgun['domain']);
			break;

			case 'mandrill':
				$mandrill = $this->app['config']['services.mandrill'];
				$this->app['swift.transport'] = new MandrillTransport($mandrill['secret']);
			break;

			default:
				throw new \InvalidArgumentException('Invalid mail driver.');
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('mailer');
	}
	
}