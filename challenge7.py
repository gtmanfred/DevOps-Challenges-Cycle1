#!/usr/bin/env python2
from __future__ import print_function
from auth import Rackspace


class Challenge7(Rackspace):
    def run(self, lb_name, server1_name, server2_name):
        cs = self.cloud_servers
        server1 = [
            server for server in cs.servers.list() if server1_name == server.name
        ]
        if server1:
            server1 = server1[0]
        else:
            server1 = self.create_server(
                name=server1_name,
                image='df27d481-63a5-40ca-8920-3d132ed643d9',
                flavor='performance1-1'
            )

        server2 = [
            server for server in cs.servers.list()
            if server2_name == server.name
        ]
        if server2:
            server2 = server2[0]
        else:
            server2 = self.create_server(
                name=server2_name,
                image='df27d481-63a5-40ca-8920-3d132ed643d9',
                flavor='performance1-1'
            )

        return self.create_loadbalancer(lb_name, server1, server2)

if __name__ == '__main__':
    this = Challenge7()
    print(this.run('test-lb', 'server1', 'server2'))
