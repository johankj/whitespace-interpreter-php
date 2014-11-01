|push:1  # 	
|label:67
  # 	    		
|dup 
 |outnum	
 	|push:10  # 	 	 
|outchar	
  |push:1  # 	
|add	   |dup 
 |push:11  # 	 		
|sub	  	|jz:69
	 # 	   	 	
|jmp:67
 
# 	    		
|label:69
  # 	   	 	
|discard 

|exit


