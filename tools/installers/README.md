# IXP Manager Install Scripts

This directory contains officially supported install scripts for IXP Manager. The file names will denote the target platform and the IXP Manager branch.

These install scripts are **opinionated**! They are intended to run exactly as specified and only on the operating system they are targeted at.

Older scripts for historic releases and supported platforms can be found in the `legacy/` directory.

## Video Tutorials

We have created a video tutorial demonstrating the installation process using the automated script for IXP Manager. You can find the [latest installation videos here](https://www.ixpmanager.org/download/install) and a complete list of available tutorials here: [https://www.ixpmanager.org/support/tutorials](https://www.ixpmanager.org/support/tutorials).

## IXP Manager v6 on Ubuntu LTS 20.04

From July 2021, the recommended IXP manager version is the v6 release and the recommend platform is Ubuntu LTS 20.04.

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

Once the installation completes, you will be given log in details. If the password provided does not work, you can reset it on the command line using:

```sh
cd /srv/ixpmanager
./artisan user:set-password --search %
```

## Getting Help

If you need assistance with this, **please watch the video tutorial referenced above first**. Then, if further help is required, please post the contents of `/tmp/ixp-manager-install.log` to [an online pastebin](https://pastebin.ibn.ie/) and then send an email to the [IXP Manager mailing list](https://www.inex.ie/mailman/listinfo/ixpmanager).


## Testing

*Mainly for development team use:*

The install script can be tested using Vagrant. Assuming you have Vagrant installed and have cloned the IXP Manager GitHub repository to `$IXPMDIR`:

```bash
cd $IXPMDIR/tools/installers
cp Vagrantfile.ubuntu-lts-2004-ixp-manager-v6 Vagrantfile
vagrant up
```

Vagrant should run `ubuntu-lts-1604-ixp-manager-v4.sh --no-interaction` as part of its bootstrapping and you should be able to access the resultant IXP Manager install at: http://localhost:8080/ (after ~10 mins).
