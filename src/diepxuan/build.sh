#!/usr/bin/env bash
#!/bin/bash

APT_CONF_FILE=/etc/apt/apt.conf.d/50build-deb-action

pwd_dir=$(dirname $(realpath "$BASH_SOURCE"))
source_dir=$pwd_dir/${MODULE:-'management'}
source_dir=$(realpath $source_dir 2>/dev/null)
source_dir=${source_dir:-$pwd_dir}

[[ -f /etc/os-release ]] && . /etc/os-release
[[ -f /etc/lsb-release ]] && . /etc/lsb-release
CODENAME=${CODENAME:-$DISTRIB_CODENAME}
CODENAME=${CODENAME:-$VERSION_CODENAME}
CODENAME=${CODENAME:-$UBUNTU_CODENAME}
CODENAME=$(echo "$CODENAME" | tr '[:upper:]' '[:lower:]')

RELEASE=${RELEASE:-$(echo $DISTRIB_DESCRIPTION | awk '{print $2}')}
RELEASE=${RELEASE:-$(echo $VERSION | awk '{print $1}')}
RELEASE=${RELEASE:-$(echo $PRETTY_NAME | awk '{print $2}')}
RELEASE=${RELEASE:-${DISTRIB_RELEASE}}
RELEASE=${RELEASE:-${VERSION_ID}}
RELEASE=$(echo "$RELEASE" | awk -F. '{print $1"."$2}')
RELEASE=$(echo "$RELEASE" | tr '[:upper:]' '[:lower:]')

DISTRIB=${DISTRIB:-$DISTRIB_ID}
DISTRIB=${DISTRIB:-$ID}
DISTRIB=$(echo "$DISTRIB" | tr '[:upper:]' '[:lower:]')

export DEBIAN_FRONTEND=noninteractive

cat | tee "$APT_CONF_FILE" <<-EOF
APT::Get::Assume-Yes "yes";
APT::Install-Recommends "no";
Acquire::Languages "none";
quiet "yes";
EOF

ln -fs /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime
apt-get install -y tzdata
dpkg-reconfigure -f noninteractive tzdata

apt-get update
apt-get install -y dpkg-dev libdpkg-perl dput tree devscripts libdistro-info-perl software-properties-common debhelper-compat
apt-get install -y build-essential debhelper fakeroot gnupg reprepro wget curl git sudo vim locales
apt-get build-dep -y -- "$source_dir"

locale-gen en_US.UTF-8
update-locale LANG=en_US.UTF-8

echo "$GPG_KEY====" | tr -d '\n' | fold -w 4 | sed '$ d' | tr -d '\n' | fold -w 76 | base64 -di | gpg --batch --import || true
gpg --list-secret-keys --keyid-format=long

version=${version:-"$(dpkg-parsechangelog -S Version -l $source_dir/debian/changelog 2>/dev/null)"}
version=${version:-"$(head -n 1 $source_dir/debian/changelog | awk -F '[()]' '{print $2}')"}
version=${version:-"0.0.0"}
new_version="${version}-${DISTRIB}-${RELEASE}"
sed -i -e "s|$version|$new_version|g" $source_dir/debian/changelog

codename_os=${codename_os:-"$(dpkg-parsechangelog -S Distribution -l $source_dir/debian/changelog 2>/dev/null)"}
codename_os=${codename_os:-"$(head -n 1 $source_dir/debian/changelog | awk '{print $3}' | sed 's|;||g')"}
codename_os=${codename_os:-"jammy"}
sed -i -e "s|$codename_os|$CODENAME|g" $source_dir/debian/changelog

cd $source_dir
dpkg-parsechangelog
dpkg-buildpackage --force-sign || dpkg-buildpackage --force-sign -d