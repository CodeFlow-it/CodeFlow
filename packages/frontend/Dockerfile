
# Use the official Node.js 18 image as the base image
FROM node:18

# Set the working directory to /app
WORKDIR /app

# Copy the package.json and package-lock.json files to the working directory
COPY package*.json ./

# Install the dependencies
RUN yarn install

# Copy the rest of the application code to the working directory
COPY . .

# Expose the port on which the application will run (if applicable)
EXPOSE 8080

# Define the command to start the application
CMD [ "yarn", "serve" ]
