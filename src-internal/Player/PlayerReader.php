<?php
namespace Icecave\Siphon\Player;

use Icecave\Siphon\Atom\AtomEntry;
use Icecave\Siphon\Util;
use Icecave\Siphon\XPath;
use Icecave\Siphon\XmlReaderInterface;

/**
 * Read data from player feeds.
 */
class PlayerReader implements PlayerReaderInterface
{
    public function __construct(XmlReaderInterface $xmlReader)
    {
        $this->xmlReader = $xmlReader;
    }

    /**
     * Read a player feed.
     *
     * @param string $sport  The sport (eg, baseball, football, etc)
     * @param string $league The league (eg, MLB, NFL, etc)
     * @param string $season The season name.
     * @param string $teamId The ID of the team.
     *
     * @return array<tuple<PlayerInterface, SeasonDetailsInterface>>
     */
    public function read($sport, $league, $season, $teamId)
    {
        $sport  = strtolower($sport);
        $league = strtoupper($league);

        $xml = $this
            ->xmlReader
            ->read(
                sprintf(
                    '/sport/v2/%s/%s/players/%s/players_%d_%s.xml',
                    $sport,
                    $league,
                    $season,
                    Util::extractNumericId($teamId),
                    $league
                )
            )
            ->xpath('//player');

        $result = [];

        foreach ($xml as $element) {
            $result[] = [
                new Player(
                    strval($element->id),
                    XPath::string($element, "name[@type='first']"),
                    XPath::string($element, "name[@type='last']")
                ),
                new SeasonDetails(
                    strval($element->id),
                    $season,
                    XPath::stringOrNull($element, "season-details/number"),
                    XPath::string($element, "season-details/position[@type='primary']/name[@type='short']"),
                    XPath::string($element, "season-details/position[@type='primary']/name[count(@type)=0]"),
                    XPath::string($element, "season-details/active") === 'true'
                ),
            ];
        }

        return $result;
    }

    /**
     * Read a feed based on an atom entry.
     *
     * @param AtomEntry $atomEntry
     *
     * @return mixed
     */
    public function readAtomEntry(AtomEntry $atomEntry)
    {
        throw new InvalidArgumentException(
            'Unsupported atom entry.'
        );
    }

    /**
     * Check if the given atom entry can be used by this reader.
     *
     * @param AtomEntry $atomEntry
     *
     * @return boolean
     */
    public function supportsAtomEntry(AtomEntry $atomEntry)
    {
        return false;
    }

    private $xmlReader;
}
