#!/bin/sh

endianness=$(printf I | hexdump -o | awk '{ print substr($2,6,1); exit}')

endian="Big"
if [ "${endianness}" = "1" ]; then
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
php --version
echo
