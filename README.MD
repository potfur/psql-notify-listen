PSQL Notify-Listen example
--------------------------

An proof of concept for Postgres _notify - listen_ functionality, in this case - every insert will trigger notification.

# How to run

Basically whole thing is set of 3 commands:

 - `db:init` - that will setup database, triggers and notifying function
 - `channel:listen` - channel listener
 - `channel:send` - sends notification by inserting new row
 

Docker example:

```cli
docker-compose up listener sender
``` 