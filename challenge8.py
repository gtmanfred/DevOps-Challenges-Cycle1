#!/usr/bin/env python2
from __future__ import print_function
import argparse
from auth import Rackspace


class Challenge8(Rackspace):
    def run(self, name):
        cm = self.cloud_monitoring
        server = self.create_server(name)

        ent = cm.create_entity(
            name="ping_test",
            ip_addresses={
                server.name: server.accessIPv4
            },
            metadata={"description": "Test Ping"}
        )

        chk = cm.create_check(
            ent,
            label="ping_check",
            check_type="remote.ping",
            details={"count": 5},
            period=60,
            target_alias=ent.ip_addresses.items()[0][0],
            monitoring_zones_poll=[cm.list_monitoring_zones()[0]],
            timeout=20
        )

        plan = cm.list_notification_plans()[0]

        alarm_check = """
if (metric['available'] < 80) {
  return new AlarmStatus(CRITICAL, 'Packet loss is greater than 20%');
}

if (metric['available'] < 95) {
  return new AlarmStatus(WARNING, 'Packet loss is greater than 5%');
}

return new AlarmStatus(OK, 'Packet loss is normal');
"""
        alarm = cm.create_alarm(
            ent,
            chk,
            plan,
            alarm_check,
            label="sample alarm"
        )

if __name__ == '__main__':
    this = Challenge8()
    this.run('linux-rackspace.package.gtmanfred.com')
