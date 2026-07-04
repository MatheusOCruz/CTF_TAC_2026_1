#include <iostream>
#include <fstream>
#include <string>
#include <cstdlib>
#include <regex>
#include <unistd.h>
#include <cstring>     // For std::strlen

#include "helper.cpp"

int main() {
    std::cout << "[SYSTEM]: In case of compilation errors, check backup code at /opt\n\n";
    
    std::cout << "[SYSTEM]: Verifying system identity...\n";

    // Id verification 
    FILE* pipe = popen("id", "r");
    if (!pipe) {
        std::cout << "Error: Identity subsystem failed.\n\n";
        return 1;
    }

    std::string id_output;
    id_output.resize(256);

    if (fgets(&id_output[0], id_output.size(), pipe) != nullptr) {
        id_output.resize(std::strlen(id_output.c_str()));
    }
    pclose(pipe);

    // std::cout << id_output << "\n";

    if (!verify_id(id_output)) {
        return 1;
    }

    std::string pass;
    std::cout << "\n[SYSTEM]: Only the REAL taxman knows my name: ";
    if (!(std::cin >> pass)) return 1;

    if (!verify_password(pass)){
        std::cout << "[SYSTEM]: I knew you weren't the REAL taxman!\n";
        std::cout << "[SYSTEM]: But just in case you are, i remember you wrote my name somewhere on your internal File Transfer service\n";
        std::cout << "[SYSTEM]: I don't remember if your password was strong, though\n";
    }

    return 0;
}
