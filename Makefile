.PHONY: install

PWD := $(shell pwd)
domain ?= $(shell basename $(PWD))

install:
	mkdir -p Logs && chmod 777 Logs
	mkdir -p storage && chmod 777 storage
	composer install
	composer dump-autoload --optimize
