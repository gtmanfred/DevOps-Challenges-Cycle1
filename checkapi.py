"""
Used to check different api json output.

curl -s -X GET -H 'Content-type: application/json' -H "X-Auth-Token: $token" $endpoint/domains | python -m checkapi totalEntries
works for python2 and python3

"""
from __future__ import print_function
import json
import sys

infile = sys.stdin
outfile = sys.stdout
with infile:
    try:
        obj = json.load(infile)
    except ValueError as e:
        raise SystemExit(e)
with outfile:
    print("{}: {}".format(sys.argv[1], obj[sys.argv[1]]), file=outfile)
