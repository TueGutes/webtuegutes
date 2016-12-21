#!/bin/sh

case "$(uname -s)" in

   Darwin)
     echo 'Dein Betriebssystem ist Mac OS X'
     ;;

   Linux)
     echo 'Dein Betriebssystem ist Linux!!'
     ;;

   CYGWIN*|MINGW32*|MSYS*)
     echo 'Dein Betriebssystem ist MS Windows'
     ;;

   # Add here more strings to compare
   # See correspondence table at the bottom of this answer

   *)
     echo 'Dein Betriebssystem ist other OS' 
     ;;
esac

vendor/bin/phinx migrate -e testing
