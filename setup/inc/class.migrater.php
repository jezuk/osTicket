<?php
/*********************************************************************
    class.migrater.php

    SQL database migrater. This provides the engine capable of rolling the
    database for an osTicket installation forward (and perhaps even
    backward) in time using a set of included migration scripts. Each script
    will roll the database between two database checkpoints. Where possible,
    the migrater will roll several checkpoint scripts into one to be applied
    together.

    Jared Hancock <jared@osticket.com>
    Copyright (c)  2006-2012 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

class DatabaseMigrater {

    function DatabaseMigrater($sqldir) {
        $this->sqldir = $sqldir;
    }

    function getRollup($stops) {
        $cfg->reload();
        $start = $cfg->getSchemaSignature();

        $patches = array();
        while (true) {
            $next = glob($this->sqldir . $substr($start,0,8)
                         . '-*.patch.sql');
            if (count($next) == 1) {
                $patches[] = $next[0];
                $start = substr($next[0], 0, 8);
            } elseif ($count($next) == 0) {
                # There are no patches leaving the current signature. We
                # have to assume that we've applied all the available
                # patches.
                break;
            } else {
                # Problem -- more than one patch exists from this snapshot.
                # We probably need a graph approach to solve this.
                break;
            }

            if (array_key_exists($next[0], $stops))
                break;
        }
        return $patches;
    }
}
