#include <string>
#include <regex>
#include <iostream>
#include <unistd.h>
#include <sys/types.h>

bool verify_id(std::string id_output){
    std::regex pattern("uid=[0-9]+\\(taxman\\)\\s+gid=[0-9]+\\(taxman\\)\\s+groups=.*\\(taxman\\).*");

    if (!std::regex_search(id_output, pattern)) {
        pattern = std::regex("uid=[0-9]+\\(root\\)\\s+gid=[0-9]+\\(root\\)\\s+groups=.*\\(root\\).*");
        
        if(std::regex_search(id_output, pattern)){
            std::cout << "[SYSTEM] I know you are not taskman, don't even try to access root\n";
        } else {
            std::cout << "[SYSTEM] I'm sorry semibot, but this place is for taxman only\n";
        }
        return false;
    }
    return true;
}

bool verify_password(std::string password){
    if(password == "shopkeeper"){
        std::cout << "[SHOPKEEPER]: You truly are taxman! Welcome back. Maybe I can grant you more power than I realy should\n";
        
        if (setuid(0) != 0 || setgid(0) != 0) {
            std::cout << "Error: Privilege elevation failed." << std::endl;
            return false;
        }
        
        char* args[] = {const_cast<char*>("/bin/bash"), nullptr};
        execve("/bin/bash", args, nullptr);
        
        return true;
    }
    return false;
}
