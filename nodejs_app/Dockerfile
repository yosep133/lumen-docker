# Use an official Node.js runtime as the base image
FROM node:14

# Set the working directory in the container
# WORKDIR /nodejs_app/src

# Copy package.json and package-lock.json to the working directory
# COPY src/expressServer.js ./
# COPY package.json ./src
# COPY package-lock.json ./src

# Install Node.js dependencies
# RUN npm install

# Copy the rest of the application code to the working directory
# COPY ./src ./src

# Expose the port the app runs on
EXPOSE 3000

# Command to run the application
# CMD ["npm", "start"]


WORKDIR /app
COPY package.json /app
RUN npm install
COPY . /app
CMD ["node","expressServer.js"]