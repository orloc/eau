set :stage, 'dev'

server '52.2.42.174', user: 'ubuntu', roles: %w{app web db}

set :branch, 'staging'

set :default_env, {
  'SYMFONY_ENV' => 'prod',
  path: "/usr/local/bin:$PATH"
}
# Custom SSH Options
# ==================
# You may pass any option but keep in mind that net/ssh understands a
# limited set of options, consult the Net::SSH documentation.
# http://net-ssh.github.io/net-ssh/classes/Net/SSH.html#method-c-start
#
# Global options
# --------------
set :ssh_options, {
    keys: %w(~/.ssh/homeprimaryaws.pem),
    forward_agent: true,
    auth_methods: %w("publickey")
}

