##nginx上.加一下配置.对options

    if ($request_method = OPTIONS ) {
      add_header Access-Control-Allow-Origin $http_origin;
      add_header Access-Control-Allow-Headers Authorization,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Data-Type,X-Requested-With;
      add_header Access-Control-Allow-Methods GET,POST,OPTIONS,HEAD,PUT;
      add_header Access-Control-Allow-Credentials true;
      add_header Access-Control-Allow-Headers X-Data-Type,X-Auth-Token; 
      return 200;
    }
        
