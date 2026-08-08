<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\Hal\View;

use Exception;
use Laminas\ApiTools\Hal\Collection;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\View\HalJsonModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use stdClass;

class HalJsonModelTest extends TestCase
{
    /** @var HalJsonModel */
    protected $model;

    public function setUp(): void
    {
        $this->model = new HalJsonModel();
    }

    public function testPayloadIsNullByDefault(): void
    {
        self::assertNull($this->model->getPayload());
    }

    public function testPayloadIsMutable(): void
    {
        $this->model->setPayload('foo');
        self::assertEquals('foo', $this->model->getPayload());
    }

    /**
     * @return array
     */
    public static function invalidPayloads()
    {
        return [
            'null'       => [null],
            'true'       => [true],
            'false'      => [false],
            'zero-int'   => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'string'     => ['string'],
            'array'      => [[]],
            'stdclass'   => [new stdClass()],
        ];
    }

    /**
     * @return array<string,array<array-key,mixed>>
     */
    public static function invalidCollectionPayloads()
    {
        $payloads              = self::invalidPayloads();
        $payloads['exception'] = [new Exception()];
        $payloads['stdclass']  = [new stdClass()];
        $payloads['hal-item']  = [new Entity([], 'id')];
        return $payloads;
    }

    /**
     * @param mixed $payload
     */
    #[DataProvider('invalidCollectionPayloads')]
    public function testIsCollectionReturnsFalseForInvalidValues($payload): void
    {
        $this->model->setPayload($payload);
        self::assertFalse($this->model->isCollection());
    }

    public function testIsCollectionReturnsTrueForCollectionPayload(): void
    {
        $collection = new Collection([], 'item/route');
        $this->model->setPayload($collection);
        self::assertTrue($this->model->isCollection());
    }

    /**
     * @return array<string,array<array-key,mixed>>
     */
    public static function invalidEntityPayloads()
    {
        $payloads                   = self::invalidPayloads();
        $payloads['exception']      = [new Exception()];
        $payloads['stdclass']       = [new stdClass()];
        $payloads['hal-collection'] = [new Collection([], 'item/route')];
        return $payloads;
    }

    /**
     * @param mixed $payload
     */
    #[DataProvider('invalidEntityPayloads')]
    public function testIsEntityReturnsFalseForInvalidValues($payload): void
    {
        $this->model->setPayload($payload);
        self::assertFalse($this->model->isEntity());
    }

    public function testIsEntityReturnsTrueForEntityPayload(): void
    {
        $item = new Entity([], 'id');
        $this->model->setPayload($item);
        self::assertTrue($this->model->isEntity());
    }

    public function testIsTerminalByDefault(): void
    {
        self::assertTrue($this->model->terminate());
    }

    #[Depends('testIsTerminalByDefault')]
    public function testTerminalFlagIsNotMutable(): void
    {
        $this->model->setTerminal(false);
        self::assertTrue($this->model->terminate());
    }
}
