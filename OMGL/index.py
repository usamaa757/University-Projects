import pygame
import random

# Initialize Pygame
pygame.init()

# Screen dimensions
WIDTH = 800
HEIGHT = 800
screen = pygame.display.set_mode((WIDTH, HEIGHT))
pygame.display.set_caption("Random Maze with Locks")

# Colors
BLACK = (0, 0, 0)
RED = (255, 0, 0)
GREEN = (0, 255, 0)
BLUE = (0, 0, 255)
WHITE = (255, 255, 255)

# Grid settings
CELL_SIZE = 32
GRID_WIDTH = 25
GRID_HEIGHT = 25

# Load lock images (replace with your actual image paths)
lock_brown = pygame.image.load("brown_lock.png")
lock_teal = pygame.image.load("teal_lock.png")
lock_purple = pygame.image.load("purple_lock.png")
heart_pink = pygame.image.load("pink_heart.png")

# Scale images to fit the cell size
lock_brown = pygame.transform.scale(lock_brown, (CELL_SIZE, CELL_SIZE))
lock_teal = pygame.transform.scale(lock_teal, (CELL_SIZE, CELL_SIZE))
lock_purple = pygame.transform.scale(lock_purple, (CELL_SIZE, CELL_SIZE))
heart_pink = pygame.transform.scale(heart_pink, (CELL_SIZE, CELL_SIZE))

# Player settings
PLAYER_SIZE = CELL_SIZE // 2
player_x = 0
player_y = 0
player_speed = CELL_SIZE
keys_collected = 0  # Player's key count

# Maze generation (Recursive Backtracking)
def generate_maze(width, height):
    maze = [[1] * width for _ in range(height)]  # Initialize with walls
    stack = [(0, 0)]
    maze[0][0] = 0  # Start cell is open

    while stack:
        x, y = stack[-1]
        neighbors = []

        # Check valid neighbors
        if x > 1 and maze[y][x - 2] == 1:
            neighbors.append((x - 2, y))
        if x < width - 2 and maze[y][x + 2] == 1:
            neighbors.append((x + 2, y))
        if y > 1 and maze[y - 2][x] == 1:
            neighbors.append((x, y - 2))
        if y < height - 2 and maze[y + 2][x] == 1:
            neighbors.append((x, y + 2))

        if neighbors:
            nx, ny = random.choice(neighbors)
            maze[ny][nx] = 0  # Open neighbor cell
            maze[ny + (y - ny) // 2][nx + (x - nx) // 2] = 0  # Open wall between cells
            stack.append((nx, ny))
        else:
            stack.pop()

    return maze

# Generate maze
maze = generate_maze(GRID_WIDTH, GRID_HEIGHT)

# Randomly place lock gates
num_locks = int(GRID_WIDTH * GRID_HEIGHT * 0.2)  # Adjust density of locks
lock_positions = []
lock_types = {}
for _ in range(num_locks):
    x = random.randint(0, GRID_WIDTH - 1)
    y = random.randint(0, GRID_HEIGHT - 1)
    if maze[y][x] == 0 and (x, y) != (0, 0) and (x, y) != (GRID_WIDTH - 1, GRID_HEIGHT - 1):  # Avoid placing locks on start/end
        maze[y][x] = 1
        lock_positions.append((y, x))  # Store the lock positions
        lock_type = random.choice(["brown", "teal", "purple", "heart"])
        lock_types[(y, x)] = lock_type

# Start and end positions
start_pos = (0, 0)
end_pos = (GRID_HEIGHT - 1, GRID_WIDTH - 1)

# Main game loop
running = True
while running:
    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            running = False
        elif event.type == pygame.KEYDOWN:
            new_player_x, new_player_y = player_x, player_y
            if event.key == pygame.K_LEFT:
                new_player_x -= player_speed
            elif event.key == pygame.K_RIGHT:
                new_player_x += player_speed
            elif event.key == pygame.K_UP:
                new_player_y -= player_speed
            elif event.key == pygame.K_DOWN:
                new_player_y += player_speed

            # Collision detection
            grid_x = new_player_x // CELL_SIZE
            grid_y = new_player_y // CELL_SIZE

            if 0 <= grid_x < GRID_WIDTH and 0 <= grid_y < GRID_HEIGHT:
                if maze[grid_y][grid_x] == 0:
                    # Check for unlocking locks
                    if (grid_y, grid_x) in lock_types:
                        lock_type = lock_types[(grid_y, grid_x)]
                        if lock_type == "brown" and keys_collected > 0:  # Example condition
                            del lock_types[(grid_y, grid_x)]  # Unlock the lock

                    player_x, player_y = new_player_x, new_player_y

    # Draw the maze
    screen.fill(BLACK)
    for i in range(GRID_HEIGHT):
        for j in range(GRID_WIDTH):
            rect = pygame.Rect(j * CELL_SIZE, i * CELL_SIZE, CELL_SIZE, CELL_SIZE)
            pygame.draw.rect(screen, RED, rect, 1)  # Draw grid lines

            if maze[i][j] == 1:
                lock_type = lock_types.get((i, j))
                if lock_type == "brown":
                    screen.blit(lock_brown, rect)
                elif lock_type == "teal":
                    screen.blit(lock_teal, rect)
                elif lock_type == "purple":
                    screen.blit(lock_purple, rect)
                elif lock_type == "heart":
                    screen.blit(heart_pink, rect)

    # Draw player
    pygame.draw.circle(screen, GREEN, (player_x + PLAYER_SIZE, player_y + PLAYER_SIZE), PLAYER_SIZE)

    # Draw start and end markers
    pygame.draw.circle(screen, GREEN, (start_pos[1] * CELL_SIZE + CELL_SIZE // 2, start_pos[0] * CELL_SIZE + CELL_SIZE // 2), CELL_SIZE // 4)
    pygame.draw.circle(screen, BLUE, (end_pos[1] * CELL_SIZE + CELL_SIZE // 2, end_pos[0] * CELL_SIZE + CELL_SIZE // 2), CELL_SIZE // 4)

    # Draw keys collected (if you want to keep track of keys, you can add a static number)
    font = pygame.font.Font(None, 36)
    text = font.render(f'Keys: {keys_collected}', True, WHITE)
    screen.blit(text, (10, 10))

    # Check for win condition
    if (player_x // CELL_SIZE, player_y // CELL_SIZE) == end_pos:
        win_text = font.render("You Win!", True, GREEN)
        screen.blit(win_text, (WIDTH // 2 - 50, HEIGHT // 2 - 20))

    # Update the display
    pygame.display.flip()

# Quit Pygame
pygame.quit()