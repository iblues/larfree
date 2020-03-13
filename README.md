<h1 align="center"> larfree </h1>

<p align="center"> 快速开发套件.</p>


## Installing

```shell
$ composer require iblues/larfree -vvv
```

## Usage

TODO

中间件

    'api' => [
        ...,
        \App\Http\Middleware\ApiFormat::class,
    ],
    
    
    protected $routeMiddleware = [
        ...,
        'wechat.oauth' => \Overtrue\LaravelWeChat\Middleware\OAuthAuthenticate::class,
    ]
## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/iblues/larfree/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/iblues/larfree/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT


LarfreeRepository
新增$advFieldSearch

    $advFieldSearch=['*']
    $advFieldSearch=['id','name']
    $advFieldSearch=null


高级查询

    name=$%123%
    name=>|123,<123
    name=>$123|<123     >123 or <123
    name=$[1,2,3]
    name=![1,2,3]          name not in [1,2,3] or id =1 
    name=|[1,2,3]&id=|1    name in [1,2,3] or id =1 
    

```shell script
    docker run -d -p 3000:3000 --name metabase metabase/metabase
or
    docker run  -p 3000:3000 \
    --cpus=1 \
    -e "MB_DB_TYPE=mysql" \
    -e "MB_DB_DBNAME=metabase" \
    -e "MB_DB_PORT=3306" \
    -e "MB_DB_USER=parrot_demo_54bl" \
    -e "MB_DB_PASS=MByXbdmn6kWs8xH5" \
    -e "MB_DB_HOST=rm-2ze5s9bc2ocin3k52ko.mysql.rds.aliyuncs.com" \
    --name metabase metabase/metabase

```