# COLORS
GREEN		= \033[1;32m
RED 		= \033[1;31m
ORANGE		= \033[1;33m
CYAN		= \033[1;36m
RESET		= \033[0m

# FOLDER
SRCS_DIR	= ./
DOCKER_DIR	= ${SRCS_DIR}docker-compose.yml


# COMMANDS
DOCKER		=  docker compose -f ${DOCKER_DIR} -p iktickets

%:
	@:

all: up

start: up

up:
	@echo "${GREEN}Starting containers...${RESET}"
	@${DOCKER} up -d --remove-orphans

down:
	@echo "${RED}Stopping containers...${RESET}"
	@${DOCKER} down

stop:
	@echo "${RED}Stopping containers...${RESET}"
	@${DOCKER} stop

rebuild:
	@echo "${GREEN}Rebuilding containers...${RESET}"
	@${DOCKER} up -d --remove-orphans --build

delete:
	@echo "${RED}Deleting containers...${RESET}"
	@${DOCKER} down -v --remove-orphans

node:
	@echo "${GREEN}Running nodejs...${RESET}"
	@${DOCKER} exec nodejs bash

dev:
	@echo "${GREEN}Running watch...${RESET}"
	@touch ./app/.dev
	@${DOCKER} exec nodejs yarn run dev

production:
	@echo "${GREEN}Compiling iktickets for production...${RESET}"
	@rm -f ./app/.dev
	@${DOCKER} exec nodejs yarn run production

yarn:
	@echo "${GREEN}Running yarn...${RESET}"
	@${DOCKER} exec nodejs yarn $(filter-out $@,$(MAKECMDGOALS))

.PHONY: all up down stop rebuild delete node dev production yarn