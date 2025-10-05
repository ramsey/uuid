# Configuration file for the Sphinx documentation builder.
#
# This file only contains a selection of the most common options. For a full
# list see the documentation:
# https://www.sphinx-doc.org/en/master/usage/configuration.html

# -- Path setup --------------------------------------------------------------

# If extensions (or modules to document with autodoc) are in another directory,
# add these directories to sys.path here. If the directory is relative to the
# documentation root, use os.path.abspath to make it absolute, like shown here.

import os
import sphinx_rtd_theme
import sys
import datetime

from pygments.lexers.web import PhpLexer
from sphinx.highlighting import lexers
from subprocess import Popen, PIPE

def get_version():
    if os.environ.get('READTHEDOCS') == 'True':
        return os.environ.get('READTHEDOCS_VERSION')

    pipe = Popen('git branch | grep \\*', stdout=PIPE, shell=True, universal_newlines=True)
    version = pipe.stdout.read()

    if version:
        return version[2:]
    else:
        return 'unknown'


# -- Project information -----------------------------------------------------

project = 'ramsey/uuid'
copyright = '2012-{year}, Ben Ramsey'.format(year = datetime.date.today().strftime('%Y'))
author = 'Ben Ramsey'

version = get_version().strip()
release = version

today = datetime.date.today().strftime('%Y-%m-%d')


# -- General configuration ---------------------------------------------------

master_doc = 'index'
highlight_language = 'php'

# enable highlighting for PHP code not between ``<?php ... ?>`` by default
lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)

# Add any Sphinx extension module names here, as strings. They can be
# extensions coming with Sphinx (named 'sphinx.ext.*') or your custom
# ones.
extensions = [
    'sphinx.ext.autodoc',
    'sphinx.ext.todo',
    'sphinxcontrib.phpdomain',
]

# Add any paths that contain templates here, relative to this directory.
templates_path = ['_templates']

# List of patterns, relative to source directory, that match files and
# directories to ignore when looking for source files.
# This pattern also affects html_static_path and html_extra_path.
exclude_patterns = ['_build', 'Thumbs.db', '.DS_Store']

pygments_style = 'sphinx'


# -- Options for HTML output -------------------------------------------------

# The theme to use for HTML and HTML Help pages.  See the documentation for
# a list of builtin themes.
#
html_theme = "sphinx_rtd_theme"
html_theme_options = {
    'collapse_navigation': False,
    'display_version': False
}

# Add any paths that contain custom static files (such as style sheets) here,
# relative to this directory. They are copied after the builtin static files,
# so a file named "default.css" will overwrite the builtin "default.css".
html_static_path = ['_static']

html_title = "ramsey/uuid %s Manual" % get_version()
html_show_sphinx = False

htmlhelp_basename = 'ramsey-uuid-doc'

html_context = {
    "display_github": True,
    "github_user": "ramsey",
    "github_repo": "uuid",
    "github_version": version,
    "conf_py_path": "/docs/",
}

current_year = datetime.date.today().strftime('%Y')
rst_prolog = """
.. |current_year| replace:: {0}
""".format(current_year)
