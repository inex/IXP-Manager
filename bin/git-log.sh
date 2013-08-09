#!/bin/sh

git log --date=short "--format=format:%s (%H - %an - %ad)"  | grep "^\["

