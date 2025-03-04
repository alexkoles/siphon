<?php

namespace Icecave\Siphon\Player\Injury;

use Eloquent\Enumeration\AbstractEnumeration;

class InjuryStatus extends AbstractEnumeration
{
    const PROBABLE          = 'probable';     // approximately 75% chance to play
    const QUESTIONABLE      = 'questionable'; // approximately 50% chance to play
    const DOUBTFUL          = 'doubtful';     // approximately 25% chance to play
    const OUT               = 'out';          // not scheduled to play

    // MLB ...
    const DISABLED_7_DAY    = '7-day DL';
    const DISABLED_15_DAY   = '15-day DL';
    const DISABLED_60_DAY   = '60-day DL';

    // NBA, no longer used ...
    const INJURED_RESERVE   = 'I-R';
    const INJURED_LIST      = 'I-L';

    // Canadian Football? ...
    const INJURED_9_WEEK    = 'Nine-Week Injured List';
    const INJURED_6_GAME    = 'Six-Game Injured List';
}
