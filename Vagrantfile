# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # virtualbox box config
  config.vm.box = "ubuntu/trusty32"

  # set up provisioning
  config.vm.provision :shell, path: "vagrant/provision.sh"

  # add host's default ssh key and .gitconfig to the vagrant machine
  config.vm.provision :file, source: "~/.ssh/id_rsa", destination: "~/.ssh/id_rsa"
  config.vm.provision :file, source: "~/.gitconfig", destination: "~/.gitconfig"

  # start apache server on boot
  config.vm.provision "shell", inline: "service nginx restart", run: "always"

  # set the hostname
  config.vm.hostname = "laravel-app"

  # enable port forwarding with collision detection
  config.vm.network "forwarded_port", guest: 80, host: 8080, auto_correct: true

  # set synced folder
  config.vm.synced_folder ".", "/home/vagrant/laravel"
  #owner: "vagrant", group: "apache", :mount_options => ["dmode=777", "fmode=666"]

end
