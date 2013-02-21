#!/bin/bash

git log --date=short "--format=format:%s (%h - %an - %ad)"  | grep "^\["

