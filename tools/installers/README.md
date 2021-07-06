# IXP Manager Install Scripts

This directory contains officially supported install scripts for IXP Manager. The file names will denote the target platform and the IXP Manager branch.

These install scripts are **opinionated**! They are intended to run exactly as specified and only on the operating system they are targeted at.

## Video Tutorials

We created a *rough and ready* video tutorial demonstrating the installation process using the automated script for IXP Manager v5.5.0 (May 2020) on Ubuntu LTS 20.04. The procedure is similar for v6.0 - just be sure to use the [ubuntu-lts-2004-ixp-manager-v6.sh](https://github.com/inex/IXP-Manager/blob/release-v6/tools/installers/ubuntu-lts-2004-ixp-manager-v6.sh) script. You can find the [video here](https://www.youtube.com/watch?v=U123-E8D-AI) in our [YouTube channel](https://www.youtube.com/channel/UCeW2fmMTBtE4fnlmg-2-evA).

## IXP Manager v6 on Ubuntu LTS 20.04

From July 2021, the recommended platform for the v6 branch of IXP Manager is Ubuntu LTS 20.04. Before then it was the v5 branch on Ubuntu LTS 20.04.

To install on this platform, please proceed as follows:

1. Prepare a physical / virtual machine with (minimum) 8GB of disk space (40GB recommended) and 2GB of RAM (4GB recommended). We recommend using LVM to partition your hard drive so space can be increased on the fly. We also recommend created a dedicated `/srv` partition of >= 30GB in which to install IXP Manager.
2. Attach / insert the latest [Ubuntu 20.04 LTS](http://releases.ubuntu.com/20.04/) 64-bit PC (AMD64) server install image and boot.
3. Follow the Ubuntu installers process (see our video of this referenced above for help).
4. When you reach the *SSH Setup* screen, check the *Install OpenSSH server* so you can ssh in to complete the process later.
5. Complete the installation and reboot.
6. When your new server has rebooted, log in and: 

```bash
# change to root user
sudo su -

# download the installation script
wget https://github.com/inex/IXP-Manager/raw/master/tools/installers/ubuntu-lts-2004-ixp-manager-v6.sh

# and execute it:
bash ./ubuntu-lts-2004-ixp-manager-v6.sh
```

Once the installaction completes, you will be given log in details. If the password provided does not work, you can reset it on the command line using:

```sh
cd /srv/ixpmanager
./artisan user:set-password --search %
```


## Testing

The install script can be tested using Vagrant. Assuming you have Vagrant installed and have cloned the IXP Manager GitHub repository to `$IXPMDIR`:

```bash
cd $IXPMDIR/tools/installers
cp Vagrantfile.ubuntu-lts-2004-ixp-manager-v6 Vagrantfile
vagrant up
```

Vagrant should run `ubuntu-lts-1604-ixp-manager-v4.sh --no-interaction` as part of its bootstrapping and you should be able to access the resultant IXP Manager install at: http://localhost:8080/ (after ~10 mins).

However, for an as yet unknown reason, the bootstrapping fails. Instead, you can log in with `vagrant ssh` and kick off the installer using:

    sudo /tmp/vagrant-shell --no-interaction



## Getting Help

If you need assistance with this, **please watch the video tutorial referenced above first**. Then, if further help is required, please post the contents of `/tmp/ixp-manager-install.log` to [an online pastebin](https://pastebin.ibn.ie/) and then send an email to the [IXP Manager mailing list](https://www.inex.ie/mailman/listinfo/ixpmanager).
    
   
