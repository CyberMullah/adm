# Application Nature

According the propsed domain the below are the main charecters of application:

## Intension
The proposed application is **Read / write Intensive** system which frequently recieve orders or new customers which place orders (write operation) and allow generating reports based on products, customer types (read operations).

## Real-time Data Processing 
Customers will interact with the application in real-time, it requires support for real-time data processing to handle transactions such as purchases, ratings, and reviews. The system should be available most of the times to respond to the users’ requests, this will be achieved by a master-slave architecture, where the master could be re-elected at any time if the system fails.

## Availability
High availability is required for customer interactions.

## Scalability
Since the application is expected to serve a large number of customers, it must be designed to scale to accommodate the increasing load and volume of data.
