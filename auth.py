from __future__ import print_function
import pyrax
import os.path
import ConfigParser
import time

class Rackspace(object):
    def __init__(self):
        config = ConfigParser.ConfigParser()
        settings = os.path.expanduser('~/.rackspace_cloud_credentials')
        config.read(settings)
        self.pyrax = pyrax
        self.pyrax.set_setting(
            'identity_type',
            config.get('rackspace','identity_type')
        )
        self.pyrax.set_setting(
            'region',
            config.get('rackspace', 'region')
        )
        self.pyrax.set_setting(
            'tenant_id',
            config.get('rackspace', 'tenant_id')
        )
        self.pyrax.set_credentials(
            config.get('rackspace', 'username'),
            config.get('rackspace', 'api_key')
        )
        self.cloud_databases = self.pyrax.connect_to_cloud_databases()
        self.cloud_servers = self.pyrax.connect_to_cloudservers()
        self.cloud_loadbalancers = self.pyrax.connect_to_cloud_loadbalancers()

    def get_cdb(self, name):
        for db in self.cloud_databases.list():
            if db.name == name:
                return db
        return False

    def check_status(self, inst_name, status='ACTIVE'):
        while self.get_cdb(inst_name).status != status:
            time.sleep(1)

    def get_cdb_hostname(self, inst_name):
        return self.get_cdb(inst_name).hostname

    def get_databases(self, inst_name):
        return self.get_cdb(inst_name).list_databases()

    def get_users(self, inst_name):
        return self.get_cdb(inst_name).list_users()

    def clb(self):
        return self.cloud_databases

    def cs(self):
        return self.cloud_servers


    def cdb(self):
        return self.cloud_databases
