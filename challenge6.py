#!/usr/bin/env python2
from __future__ import print_function
from auth import Rackspace
import argparse

class Challenge6(Rackspace):
    def __init__(self, instance, backup):
        super(Challenge6, self).__init__()
        self.inst_name = instance
        self.database = backup
        self.instance = self.get_cdb(self.inst_name)

    def create_backup(self):
        return self.instance.create_backup(self.database)

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='Challenge 6: DBaaS part 2')
    parser.add_argument(
        '--instance',
        type=str,
        required=True,
        help='The name of the Cloud DB instance.'
    )
    parser.add_argument(
        '--database',
        type=str,
        required=True,
        help='The name of the database to backup.'
    )
    args = parser.parse_args()
    this = Challenge6(args.instance, args.database)
    print(this.create_backup())
