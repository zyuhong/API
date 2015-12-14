.PHONY: install

PWD := $(shell pwd)
domain ?= $(shell basename $(PWD))

install:
	mkdir -p Logs && chmod 777 Logs
	composer install
