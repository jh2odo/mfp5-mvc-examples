Vagrant.configure("2") do |config|

    config.vm.define :mfp5, primary: true do |mfp5|
        mfp5.vm.box = "lucid32"
        # mfp5.vm.box_url = "http://files.vagrantup.com/lucid32.box"
        
        mfp5.vm.synced_folder "sources/", "/var/www", create: true, group: "www-data", owner: "www-data"
        mfp5.vm.provision "shell" do |s|
            s.path = "provision/mfp5.sh"
        end
        mfp5.vm.hostname = "mfp5.dev"

        # Setup port forwarding
        mfp5.vm.network :private_network, ip: "10.0.0.10"
        
        # mfp5.vm.network :forwarded_port, guest: 80, host: 8080, auto_correct: true
        # mfp5.vm.network :public_network, ip: "192.168.1.101", bridge: 'wlan0'
    end

  config.vm.provider "virtualbox" do |v|
    v.memory = 1024
    v.cpus = 1
  end

end