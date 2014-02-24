#!/usr/bin/env python2
from __future__ import print_function
from auth import Rackspace
import argparse
import urllib2


class Challenge5(Rackspace):
    def _randword(self):
        randword = urllib2.urlopen(
            'http://randomword.setgetgo.com/get.php'
        )
        return randword.read().strip()

    def flavors(self):
        return self.cloud_databases.list_flavors()

    def check_name(self, name):
        if self.get_cdb(name):
            return True
        return False

    def create(self, name, flavor=2, volume=1):
        if self.check_name(name):
            num = 1
            while self.check_name(name + str(num)):
                num += 1
            ans = input(
                (
                    '{0} is already in use, '
                    'what about (Y/n): {1}\n'
                ).format(name, name + str(num))
            )
            if ans in 'Nn':
                return False
            name += str(num)
        db = self.cloud_databases.create(name, flavor, volume)
        self.check_status(name)
        return db.name

    def create_database(self, inst, db_name):
        return inst.create_database(db_name)

    def create_user(self, inst, user_name, password='', db_names=[]):
        if not db_names:
            db_names = inst.get_databases()
        return inst.create_user(user_name, password, db_names)

    def create_databases(self, inst_name, number):
        inst = self.get_cdb(inst_name)
        dbs = []
        users = []
        while number > 0:
            db_name = self._randword()
            dbs.append(self.create_database(inst, db_name))
            users.append(self.create_user(
                inst,
                user_name=db_name,
                password='challenge',
                db_names=[db_name]
            ))
            number -= 1
        return dbs

if __name__ == '__main__':
    this = Challenge5()
    parser = argparse.ArgumentParser(description='Challenge 5: DBaaS')
    parser.add_argument(
        '--number',
        type=int,
        default=1,
        help='The number of users and databases to create.'
    )
    parser.add_argument(
        'name',
        type=str,
        help='The name of the instance to create'
    )
    args = parser.parse_args()
    name = this.create(args.name)
    this.create_databases(name, args.number)
    print(this.get_cdb_hostname(name))
