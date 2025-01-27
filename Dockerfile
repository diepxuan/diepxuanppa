ARG BASE_IMAGE=ubuntu:20.04
FROM ${BASE_IMAGE}

# Đặt timezone để tránh yêu cầu nhập liệu khi cài đặt package
ENV DEBIAN_FRONTEND=noninteractive

# Cập nhật hệ thống và cài đặt các công cụ cần thiết
RUN apt-get update && apt-get install -y \
    build-essential \
    devscripts \
    debhelper \
    fakeroot \
    gnupg \
    reprepro \
    wget \
    curl \
    git \
    sudo \
    && apt-get clean

# Cập nhật hệ điều hành và cài đặt công cụ cơ bản
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y build-essential git wget curl vim sudo locales

# Thiết lập múi giờ và ngôn ngữ
RUN apt-get update && apt-get install -y tzdata locales && \
    ln -fs /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    locale-gen en_US.UTF-8 && \
    update-locale LANG=en_US.UTF-8

# Thiết lập biến môi trường
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8

# Thiết lập thư mục làm việc
ARG WORKDIR=/src
WORKDIR ${WORKDIR}

# Định nghĩa lệnh chạy chính
# CMD ["/bin/bash"]

# Command mặc định khi container chạy
CMD ["dpkg-buildpackage", "--force-sign"]