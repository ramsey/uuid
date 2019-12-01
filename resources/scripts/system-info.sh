#!/bin/bash

endianness=$(echo -n I | od -to2 | awk '{ print substr($2,6,1); exit}')

endian="Big"
if [ $endianness -eq 1 ]; then
    endian="Little"
fi

echo
echo "SYSTEM INFORMATION:"
echo
echo "$(uname -a)"
echo
echo "CPU mode: $(getconf LONG_BIT)-bit"
echo "Endianness: ${endian}"
echo
