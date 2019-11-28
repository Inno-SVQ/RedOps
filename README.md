# RedOps

RedOps is a open source web based platform that allows you to perform and manage Red Teaming operations.

## Features:

  - Create audits (projects)
  - Add Companies to audit
  - Add Domains to audit
  - Search domains from a company
  - Search subdomains from a domain
  - Find open ports from a host and clasify services
  - Identify technologies from a web service
  - Directory fuzzing from a webpage


You can also:
  - Export information 
  - Visualize metrics an KPIs

## Arch

RedOps consists on three main parts:
* Main Server: It is the main server wich has a web server with the GUI to interoperate with the system. This server stores all the information of the audits. This server is made with Laravel and with Gentalella front template. In order to improve performance doing jobs, the web server uses websockets, to notify the UI.
* Agents: An agent is a piece of software that acts as an executor of the jobs queued by the audit in order to do some tasks. In order to execute these tasks, an agent invokes modules to execute
* Modules: A module is a simple script that executes a jobs and returns some objects found during the execution.

[![Architecture](https://github.com/Inno-SVQ/RedOps/blob/master/architecture.jpg)](https://github.com/Inno-SVQ/RedOps/blob/master/architecture.jpg)


## Requirements

For the correct operation of RedOps, the following requirements are necessary.

| Requirement | Link |
| ------ | ------ |
| MariaDB | https://mariadb.org/ |
| PHP 7 | https://www.php.net/ |
| Composer | https://getcomposer.org/ |
| Bower | https://bower.io/ |
| Gulp | https://gulpjs.com/ |
| Node 11.x | https://nodejs.org/es/ |
| npm | https://www.npmjs.com/ |
| Python 3.x | https://www.python.org/ |

Also, you can see our [Wiki][WK]. 

License
----

**GNU General Public License v3.0** - [LICENSE][LS]

[LS]: <https://github.com/Inno-SVQ/RedOps/blob/master/LICENSE>
[WK]: <https://github.com/Inno-SVQ/RedOps/wiki>



