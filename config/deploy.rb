set :application, 'evetool'
set :repo_url, 'git@github.com:orloc/evetool.git'

set :deploy_to, '/srv/evetool'
set :log_level, :debug
set :pty, false

set :keep_releases, 5

set :bower_flags, '--quiet --config.interactive=false'
set :bower_roles, :web

set :symfony_env,  "prod"
set :app_path,              "app"
set :web_path,              "web"
set :log_path,              fetch(:app_path) + "/logs"
set :cache_path,            fetch(:app_path) + "/cache"
set :app_config_path,       fetch(:app_path) + "/config"

set :controllers_to_clear, ["app_*.php"]

set :linked_files,          [fetch(:app_path) + "/config/parameters.yml"]
set :linked_dirs,           [fetch(:log_path), fetch(:web_path) + "/uploads"]

set :file_permissions_paths,         [fetch(:log_path), fetch(:cache_path)]
set :file_permissions_users, ['www-data']
set :webserver_user,        "www-data"

set :permission_method,     false
set :use_set_permissions,   false

set :symfony_console_path, fetch(:app_path) + "/console"
set :symfony_console_flags, "--no-debug"

set :assets_install_path,   fetch(:web_path)
set :assets_install_flags,  '--symlink'
set :assetic_dump_flags,  ''

fetch(:default_env).merge!(symfony_env: fetch(:symfony_env))


namespace :deploy do

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      # Here we can do anything such as:
      # within release_path do
      #   execute :rake, 'cache:clear'
      # end
    end
  end

end

Rake::Task['deploy:updated'].prerequisites.delete('composer:install')

namespace :composer do
    before 'install', 'change_dir'

    desc 'Composer update'
    task :change_dir do
        on roles(:app) do
            execute "cd #{release_path}/ && composer update"
        end
    end
end