<template>
  <div class="register-container">
    <h1>Register for AMW</h1>
    <form @submit.prevent="handleRegister">
      <div>
        <label>Name</label>
        <input v-model="name" type="text" placeholder="Your Name" required />
      </div>
      <div>
        <label>Email Address</label>
        <input v-model="email" type="email" placeholder="artist@gallery.com" required />
      </div>
      <div>
        <label>Password</label>
        <input v-model="password" type="password" placeholder="••••••••" required />
      </div>
      <div>
        <label>Confirm Password</label>
        <input v-model="password_confirmation" type="password" placeholder="••••••••" required />
      </div>
      <button type="submit">Create Account</button>
    </form>
    <p>
      Already have an account? <router-link to="/login">Sign in</router-link>
    </p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    };
  },
  methods: {
    async handleRegister() {
      try {
        const response = await axios.post('http://localhost:8000/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        });
        const token = response.data.token;
        localStorage.setItem('authToken', token);
        this.$router.push('/dashboard'); // Redirige a la vista principal
      } catch (error) {
        console.error('Error registering:', error);
      }
    },
  },
};
</script>