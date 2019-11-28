import multiprocessing

bind = "127.0.0.1:8500"
workers = multiprocessing.cpu_count() * 2