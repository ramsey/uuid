# ramsey/uuid Documentation

Changes to the documentation are automatically built by [Read the Docs][] and viewable from <https://uuid.ramsey.dev>.

## Getting Started

It's probably best to do this in a virtualenv environment, so set one up first:

``` bash
pip install virtualenvwrapper
mkvirtualenv ramsey-uuid-docs
cd docs/
workon ramsey-uuid-docs
pip install -r requirements.txt
```

## Building the Docs

To build the docs, change to the `docs/` directory, and make sure you're working on the virtualenv environment created
in the last step.

``` bash
cd docs/
workon ramsey-uuid-docs
make html
```

Then, to view the docs after building them:

``` bash
open _build/html/index.html
```

[read the docs]: https://readthedocs.org
