<?php declare(strict_types=1);

namespace App\Tests;

use Nette;
use Tester;
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';


class ExampleTest extends Tester\TestCase
{

	private $container;

	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function testPresenter()
	{
		// z DI kontejneru, který vytvořil bootstrap.php, získáme instanci PresenterFactory
		$presenterFactory = $this->container->getByType(Nette\Application\IPresenterFactory::class);
		$presenter = $presenterFactory->createPresenter('Homepage');
		$presenter->autoCanonicalize = false;
		$request = new Nette\Application\Request('Homepage', 'GET', ['action' => 'default']);
		$response = $presenter->run($request);

		Assert::type(Nette\Application\Responses\TextResponse::class, $response);
		Assert::type(Nette\Bridges\ApplicationLatte\Template::class, $response->getSource());

		$html = (string) $response->getSource();
		$dom = Tester\DomQuery::fromHtml($html);
		Assert::same((string) $dom->find('h1')[0], 'Congratulations!' );
		Assert::true( $dom->has('div[id="content"]') );
	}
}

$container = \App\Bootstrap::bootForTests()
	->createContainer();

$test = new ExampleTest($container);
$test->run();
