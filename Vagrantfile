# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/focal64"

  config.vm.network "forwarded_port", guest: 80, host: 8088
  config.vm.network "forwarded_port", guest: 3306, host: 33061

  config.vm.synced_folder ".", "/vagrant/", id: "vagrant-root0",
    owner: "vagrant"

  config.vm.synced_folder "./storage", "/vagrant/storage", id: "vagrant-root1",
      owner: "vagrant",
      group: "www-data",
      mount_options: ["dmode=775,fmode=664"]

  config.vm.synced_folder "./bootstrap/cache", "/vagrant/bootstrap/cache", id: "vagrant-root4",
      owner: "vagrant",
      group: "www-data",
      mount_options: ["dmode=775,fmode=664"]


  config.vm.provider "virtualbox" do |vb|
    vb.memory = "1536"
    vb.gui = true
  end

  config.vm.provision :shell, path: "bootstrap.sh"
end
