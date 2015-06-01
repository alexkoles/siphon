<?php
namespace Icecave\Siphon\Player;

use Eloquent\Phony\Phpunit\Phony;
use Icecave\Siphon\Reader\ResponseVisitorInterface;
use Icecave\Siphon\Schedule\SeasonInterface;
use Icecave\Siphon\Sport;
use Icecave\Siphon\Team\TeamInterface;
use PHPUnit_Framework_TestCase;

class ImageResponseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->season = Phony::mock(SeasonInterface::class)->mock();
        $this->team   = Phony::mock(TeamInterface::class)->mock();

        $this->player1 = Phony::fullMock(Player::class);
        $this->player2 = Phony::fullMock(Player::class);

        $this->player1->id->returns('<team 1>');
        $this->player2->id->returns('<team 2>');

        $this->response = new ImageResponse(
            Sport::NFL(),
            $this->season,
            $this->team
        );
    }

    public function testSport()
    {
        $this->assertSame(
            Sport::NFL(),
            $this->response->sport()
        );

        $this->response->setSport(Sport::NBA());

        $this->assertSame(
            Sport::NBA(),
            $this->response->sport()
        );
    }

    public function testSeason()
    {
        $this->assertSame(
            $this->season,
            $this->response->season()
        );

        $season = Phony::mock(SeasonInterface::class)->mock();
        $this->response->setSeason($season);

        $this->assertSame(
            $season,
            $this->response->season()
        );
    }

    public function testTeam()
    {
        $this->assertSame(
            $this->team,
            $this->response->team()
        );

        $team = Phony::mock(TeamInterface::class)->mock();
        $this->response->setTeam($team);

        $this->assertSame(
            $team,
            $this->response->team()
        );
    }

    public function testIsEmpty()
    {
        $this->assertTrue(
            $this->response->isEmpty()
        );

        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');

        $this->assertFalse(
            $this->response->isEmpty()
        );
    }

    public function testCount()
    {
        $this->assertSame(
            0,
            count($this->response)
        );

        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');

        $this->assertSame(
            1,
            count($this->response)
        );
    }

    public function testGetIterator()
    {
        $this->assertEquals(
            [],
            iterator_to_array($this->response)
        );

        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');
        $this->response->add($this->player2->mock(), '<small 2>', '<large 2>');

        $this->assertEquals(
            [
                [$this->player1->mock(), '<small 1>', '<large 1>'],
                [$this->player2->mock(), '<small 2>', '<large 2>'],
            ],
            iterator_to_array($this->response)
        );
    }

    public function testAdd()
    {
        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');

        $this->assertEquals(
            [
                [$this->player1->mock(), '<small 1>', '<large 1>'],
            ],
            iterator_to_array($this->response)
        );
    }

    public function testAddDoesNotDuplicate()
    {
        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');
        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');

        $this->assertEquals(
            [
                [$this->player1->mock(), '<small 1>', '<large 1>'],
            ],
            iterator_to_array($this->response)
        );
    }

    public function testRemove()
    {
        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');
        $this->response->remove($this->player1->mock());

        $this->assertEquals(
            [],
            iterator_to_array($this->response)
        );
    }

    public function testRemoveUnknownRequest()
    {
        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');
        $this->response->remove($this->player1->mock());
        $this->response->remove($this->player1->mock());

        $this->assertEquals(
            [],
            iterator_to_array($this->response)
        );
    }

    public function testClear()
    {
        $this->response->add($this->player1->mock(), '<small 1>', '<large 1>');
        $this->response->add($this->player1->mock(), '<small 2>', '<large 2>');

        $this->response->clear();

        $this->assertTrue(
            $this->response->isEmpty()
        );
    }

    public function testAccept()
    {
        $visitor = Phony::mock(ResponseVisitorInterface::class);

        $this->response->accept($visitor->mock());

        $visitor->visitImageResponse->calledWith($this->response);
    }
}
