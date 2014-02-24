from __future__ import print_function
import pyrax
import os.path
import ConfigParser

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


    def clb(self):
        return self.cloud_databases


    def cs(self):
        return self.cloud_servers


    def cdb(self):
        return self.cloud_databases
