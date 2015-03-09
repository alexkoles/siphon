<?php
namespace Icecave\Siphon\LiveScore\Period;

use Icecave\Chrono\TimeSpan\Duration;
use Icecave\Siphon\LiveScore\LiveScoreFactoryInterface;
use Icecave\Siphon\LiveScore\StatisticsAggregator;
use Icecave\Siphon\LiveScore\StatisticsAggregatorInterface;
use Icecave\Siphon\Score\ScopeStatus;
use SimpleXMLElement;

class PeriodLiveScoreFactory implements LiveScoreFactoryInterface
{
    /**
     * @param StatisticsAggregatorInterface|null $statisticsAggregator
     */
    public function __construct(StatisticsAggregatorInterface $statisticsAggregator = null)
    {
        if (null === $statisticsAggregator) {
            $statisticsAggregator = new StatisticsAggregator;
        }

        $this->statisticsAggregator = $statisticsAggregator;
    }

    /**
     * Check if this factory supports creation of live scores for the given
     * competition.
     *
     * @param string $sport  The sport (eg, baseball, football, etc)
     * @param string $league The league (eg, MLB, NFL, etc)
     *
     * @return boolean True if the factory supports the given competition; otherwise, false.
     */
    public function supports($sport, $league)
    {
        return in_array(
            $sport,
            [
                'football',
                'basketball',
                'hockey',
            ]
        );
    }

    /**
     * Create a live score for the given competition.
     *
     * @param string           $sport  The sport (eg, baseball, football, etc)
     * @param string           $league The league (eg, MLB, NFL, etc)
     * @param SimpleXMLElement $xml    The XML document being parsed.
     *
     * @return LiveScoreInterface
     */
    public function create($sport, $league, SimpleXMLElement $xml)
    {
        $result = new PeriodLiveScore;
        $stats  = $this->statisticsAggregator->extract($xml);
        $scope  = null;

        foreach ($stats as $s) {
            $scope = new Period(
                $s->home['score'],
                $s->away['score']
            );

            $scope->setType(
                PeriodType::memberByValue($s->type)
            );

            $result->add($scope);
        }

        $resultScope = $xml->xpath('//result-scope')[0];

        if ($scope) {
            $status = ScopeStatus::memberByValue(
                strval($resultScope->{'scope-status'})
            );

            $scope->setStatus($status);
        }

        if ($resultScope->clock) {
            list($hours, $minutes, $seconds) = explode(':', $resultScope->clock);

            $result->setGameTime(
                Duration::fromComponents(0, 0, $hours, $minutes, $seconds)
            );
        }

        return $result;
    }

    private $statisticsAggregator;
}
