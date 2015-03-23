# How to contribute

Third-party patches are welcomed for adding functionality, fixing bugs and just correcting typos on IXP Manager.

We want to keep it as easy as possible to contribute changes but there are a few guidelines that we 
need contributors to follow so that we can accept them.



## Getting Started

* Make sure you have a [GitHub account](https://github.com/signup/free)
* Submit a ticket for your issue, assuming one does not already exist.
  * Clearly describe the issue including steps to reproduce when it is a bug.
  * Make sure you fill in the earliest version that you know has the issue.
* Fork the repository on GitHub

## Making Changes

* Create a topic branch from where you want to base your work.
  * This is usually the master branch.
  * Only target release branches if you are certain your fix must be on that
    branch.
  * To quickly create a topic branch based on master; `git checkout -b
    fix/master/my_contribution master`. Please avoid working directly on the
    `master` branch.
* Make commits of logical units.
* Check for unnecessary whitespace with `git diff --check` before committing.
* Make sure your commit messages reference issue numbers where appropriate.


## Submitting Changes

* Sign the [Contributor License Agreement](https://github.com/inex/IXP-Manager/wiki/Contributor-License-Agreement) (`gpg --clearsign inex-cla.txt`) and email it to ''operations (at) inex (dot) ie''.
* Push your changes to a topic branch in your fork of the repository.
* Submit a pull request to the repository in the inex organisation.

# Additional Resources

* [Contributor License Agreement](https://github.com/inex/IXP-Manager/wiki/Contributor-License-Agreement)
* [General GitHub documentation](http://help.github.com/)
* [GitHub pull request documentation](http://help.github.com/send-pull-requests/)
* [IXP Manager Mailing List](https://www.inex.ie/mailman/listinfo/ixpmanager)

