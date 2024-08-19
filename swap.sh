# /bin/sh
sudo dd if=/dev/zero of=/swapfile bs=128M count=16 # 2GB swap file
sudo chmod 600 /swapfile

sudo mkswap /swapfile

# activate
sudo swapon /swapfile
sudo swapon -s


sudo vi /etc/fstab
# add in last line: /swapfile swap swap defaults 0 0

# check mem status and available mem
sudo free -h 

# delete: sudo rm -r swapfile
# deactivate: sudo swapoff swapfile
# deactivate all: sudo swapoff -a