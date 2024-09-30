########################################################################################################################
install redis-server

    apt install redis-server
    systemctl start redis-server
    systemctl enable redis-server
    systemctl status redis-server
    composer require predis/predis

########################################################################################################################
Test:

    cd public
    php -S localhost:8000 -t public
    
    curl -X POST http://localhost:8000/api/employee -H "Content-Type: application/json" -d '{
        "provider": "provider1",
        "firstName": "John",
        "lastName": "Doe",
        "email": "john.doe@example.com"
    }'
########################################################################################################################