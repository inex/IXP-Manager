#! /bin/bash


wget -O ./oui-new.txt http://standards.ieee.org/regauth/oui/oui.txt

if [[ $0 ]]; then
    cat oui-new.txt | grep "(hex)" > oui-new-filtered.txt
    cat oui-new-filtered.txt >oui.txt
    rm oui-new-filtered.txt
    rm oui-new.txt
fi

