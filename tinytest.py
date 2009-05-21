#!/usr/bin/env python
import os, sys

def run_script():
        return os.system('./application.php ' + ' '.join(sys.argv[1:]))

if '-l' in sys.argv or '--loop' in sys.argv:
	while True:
		ret = run_script()
		if ret == 2:
			break
else:
	run_script()
