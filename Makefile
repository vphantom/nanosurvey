SRC2MD := ./node_modules/docblox2md/cli.js --skip-protected

DOCS := $(wildcard *.md */*.md)

SRC := NanoSurvey.php

help:
	echo "Targets: help, docs"

docs:	$(DOCS)

%.md:	$(SRC)
	$(SRC2MD) $@

.SILENT:	help

.PHONY:	docs
