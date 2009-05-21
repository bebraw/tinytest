#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
TinyTest - test runner
Copyright (C) 2009 Juho Vepsäläinen

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

"""

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
