<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Card;
use Hearts\CardFilter;
use PHPUnit\Framework\TestCase;

class CardFilterTest extends TestCase
{
    protected function setUp(): void
    {
        $this->cardFilter = new CardFilter('allow');
        $this->testSet = [
            new Card('C', 4),  new Card('D', 14), new Card('S', 14), new Card('D', 8),
            new Card('D', 7),  new Card('H', 5),  new Card('S', 12), new Card('D', 9),
            new Card('H', 9),  new Card('H', 7),  new Card('H', 13), new Card('D', 11),
            new Card('S', 4),  new Card('D', 10), new Card('C', 7),  new Card('S', 10),
            new Card('S', 9),  new Card('C', 12), new Card('D', 2),  new Card('C', 14),
            new Card('H', 11), new Card('C', 9),  new Card('D', 6),  new Card('C', 4),
        ];
    }

    public function testAllFiltersAreEmptyOnInstantiation(): void
    {
        $this->assertSame([], $this->cardFilterPrivate('allowedSuits'));
        $this->assertSame([], $this->cardFilterPrivate('allowedCards'));

        $this->assertSame([], $this->cardFilterPrivate('deniedSuits'));
        $this->assertSame([], $this->cardFilterPrivate('deniedCards'));
    }

    public function testInstantiateWithIncorrectValueThrowsException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $filter = new CardFilter('test');
    }

    /**
     * CardFilter->allowSuit()
     */

    public function testAllowSuitAddsSuitsToAllowedSuits(): void
    {
        $this->cardFilter->allowSuit('S');
        $expected = ['S'];
        $this->assertSame($expected, $this->cardFilterPrivate('allowedSuits'));

        $this->cardFilter->allowSuit('C', 'D');
        $expected = ['S', 'C', 'D'];
        $this->assertSame($expected, $this->cardFilterPrivate('allowedSuits'));
    }

    /**
     * CardFilter->denySuit()
     */

    public function testDenySuitAddsSuitsToDeniedSuits(): void
    {
        $this->cardFilter->denySuit('H');
        $expected = ['H'];
        $this->assertSame($expected, $this->cardFilterPrivate('deniedSuits'));

        $this->cardFilter->denySuit('D', 'S');
        $expected = ['H', 'D', 'S'];
        $this->assertSame($expected, $this->cardFilterPrivate('deniedSuits'));
    }

    /**
     * CardFilter->allowValue()
     */

    public function testAllowCardAddsCardsToAllowedCards(): void
    {
        $this->cardFilter->allowCard(new Card('S', 12));
        $expected = [new Card('S', 12)];
        $this->assertEquals($expected, $this->cardFilterPrivate('allowedCards'));

        $this->cardFilter->allowCard(new Card('C', 4), new Card('D', 8));
        $expected = [new Card('S', 12), new Card('C', 4), new Card('D', 8)];
        $this->assertEquals($expected, $this->cardFilterPrivate('allowedCards'));
    }

    /**
     * CardFilter->setValue()
     */

    public function testDenyCardAddsCardsToDeniedCards(): void
    {
        $this->cardFilter->denyCard(new Card('S', 12));
        $expected = [new Card('S', 12)];
        $this->assertEquals($expected, $this->cardFilterPrivate('deniedCards'));

        $this->cardFilter->denyCard(new Card('C', 4), new Card('H', 8));
        $expected = [new Card('S', 12), new Card('C', 4), new Card('H', 8)];
        $this->assertEquals($expected, $this->cardFilterPrivate('deniedCards'));
    }

    /**
     * CardFilter->filter()
     */

    public function testFilterRemovesDeniedSuit(): void
    {
        $this->cardFilter->denySuit('S');
        $expected = [
            new Card('C', 4),  new Card('D', 14), new Card('D', 8),
            new Card('D', 7),  new Card('H', 5),  new Card('D', 9),
            new Card('H', 9),  new Card('H', 7),  new Card('H', 13), new Card('D', 11),
            new Card('D', 10), new Card('C', 7),
            new Card('C', 12), new Card('D', 2),  new Card('C', 14),
            new Card('H', 11), new Card('C', 9),  new Card('D', 6),  new Card('C', 4),
        ];
        $this->assertEquals($expected, array_values($this->cardFilter->filter($this->testSet)));

        $this->cardFilter->denySuit('H');
        $expected = [
            new Card('C', 4),  new Card('D', 14), new Card('D', 8),
            new Card('D', 7),  new Card('D', 9),
            new Card('D', 11),
            new Card('D', 10), new Card('C', 7),
            new Card('C', 12), new Card('D', 2),  new Card('C', 14),
            new Card('C', 9),  new Card('D', 6),  new Card('C', 4),
        ];
        $this->assertEquals($expected, array_values($this->cardFilter->filter($this->testSet)));
    }

    public function testFilterRemovesDeniedCards(): void
    {
        $this->cardFilter->denyCard(new Card('H', 13));
        $supplied = [new Card('H', 9),  new Card('H', 7),  new Card('H', 13), new Card('D', 13)];
        $expected = [new Card('H', 9),  new Card('H', 7),  new Card('D', 13)];
        $this->assertEquals($expected, array_values($this->cardFilter->filter($supplied)));

        $this->cardFilter->denyCard(new Card('D', 13));
        $supplied = [new Card('H', 9),  new Card('H', 7),  new Card('H', 13), new Card('D', 13)];
        $expected = [new Card('H', 9),  new Card('H', 7)];
        $this->assertEquals($expected, array_values($this->cardFilter->filter($supplied)));
    }

    public function testFilterKeepsAllowedSuit(): void
    {
        $cardFilter = new CardFilter('deny');
        $cardFilter->allowSuit('S');
        $expected = [new Card('S', 14), new Card('S', 12), new Card('S', 4), new Card('S', 10), new Card('S', 9)];
        $this->assertEquals($expected, array_values($cardFilter->filter($this->testSet)));

        $cardFilter->allowSuit('C');
        $expected = [
            new Card('C', 4),  new Card('S', 14), new Card('S', 12),
            new Card('S', 4),  new Card('C', 7),  new Card('S', 10),
            new Card('S', 9),  new Card('C', 12), new Card('C', 14),
            new Card('C', 9),  new Card('C', 4),
        ];
        $this->assertEquals($expected, array_values($cardFilter->filter($this->testSet)));
    }

    public function testFilterKeepsAllowedCards(): void
    {
        $cardFilter = new CardFilter('deny');
        $cardFilter->allowCard(new Card('S', 4));
        $expected = [new Card('S', 4)];
        $this->assertEquals($expected, array_values($cardFilter->filter($this->testSet)));

        $cardFilter->allowCard(new Card('C', 12));
        $expected = [new Card('S', 4),  new Card('C', 12)];
        $this->assertEquals($expected, array_values($cardFilter->filter($this->testSet)));
    }

    public function testFilterKeepsAllowedSuitButRemovesDeniedCard(): void
    {
        $cardFilter = new CardFilter('deny');
        $cardFilter->allowSuit('S');
        $cardFilter->denyCard(new Card('S', 12));
        $expected = [new Card('S', 14), new Card('S', 4), new Card('S', 10), new Card('S', 9)];
        $this->assertEquals($expected, array_values($cardFilter->filter($this->testSet)));
    }

    public function testFilterDefersToNextStage(): void
    {
        $this->cardFilter->denySuit('S', 'H', 'D', 'C');
        $this->cardFilter->appendStage(new CardFilter('allow'));
        $this->assertEquals($this->testSet, $this->cardFilter->filter($this->testSet));
    }

    /**
     * CardFilter->appendStage()
     */

    public function testAppendStage(): void
    {
        $this->cardFilter->appendStage(new CardFilter());
        $this->assertInstanceOf(CardFilter::class, $this->cardFilterPrivate('nextStage'));
    }

    /**
     * CardFilter->prependStage()
     */

    public function testPrependStage(): void
    {
        $expected = $this->cardFilter;
        $this->cardFilter = $this->cardFilter->prependStage(new CardFilter());
        $this->assertSame($expected, $this->cardFilterPrivate('nextStage'));
    }

    private function cardFilterPrivate(string $property)
    {
        $reflection = new \ReflectionClass($this->cardFilter);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($this->cardFilter);
    }
}
