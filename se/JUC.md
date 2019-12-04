## thead and process

1. definition

2. muti thread state: thread.start() will not start immedately

   - NEW
   - RUNNABLE
   - WAITING: alwys
   - TIMED_WAITING: Outdated
   - TERMINATED

3. wait and sleep

   - wait: will surrend control, then will preempt cpu with other processes
   - sleep: will donot hand over control, this process will still work imedately when sleep time is over

4. parallel and concurrency
   - concurrency: many process compare for one kind resource
   - parallel: one process do many things at same time

## quick start

1. thread -- operation -- resources
   - resources = instance var + instance method
2. high cohesion[resource property] -- low coupling
   - `Put the operation of the resource class on the resource itself to prevent contention`
3. judge -- work -- notify
4. synchronized method likes table lock; synchronized block code likes line lock
5. muti thread should use `while` as judeg condition other than `if`

   - `if` case suit for two status, if one wait, another will get access to execute
   - `while` will always to judge each time

6. demo code

   - 3 seller sale 30 tickets with juc

   ```java
   public class SaleTickets {
       public static void main(String[] args) {
           Ticket tickets = new Ticket();

           new Thread(() -> {for (int i = 1; i < 400; i++) tickets.sale();}, "seller01").start();
           new Thread(() -> {for (int i = 1; i < 400; i++) tickets.sale();}, "seller02").start();
           new Thread(() -> {for (int i = 1; i < 400; i++) tickets.sale();}, "seller03").start();
           new Thread(() -> {for (int i = 1; i < 400; i++) tickets.sale();}, "seller04").start();
       }
   }
       // resources = instance var + instance method
   class Ticket {
       private static final Logger LOG = LoggerFactory.getLogger(Ticket.class);
       private int number = 300;
       private Lock lock = new ReentrantLock();

       public synchronized void sale() {
           lock.lock();
           try {
               if (number > 0) {
                   LOG.info( Thread.currentThread().getName() + " sale ticket number: " + number-- + " ," + number + " tickets left.");
               }
           } finally {
               lock.unlock();
           }
       }
   }
   ```

   - Two or many threads alternately modify a variable

   ```java
   public class NotifyWait {
       public static void main(String[] args) {
           NotifyWait notifyWait = new NotifyWait();
           notifyWait.NoVirtualWake();
           notifyWait.VirtualWake();
       }

       public void NoVirtualWake() {
           ShareDataVersion1 data = new ShareDataVersion1();
           increaseData(data, "increase-thread").start();
           decreaseData(data, "decrease-thread").start();
       }

       public void VirtualWake() {
           // handle with while to judge
           ShareDataVersion1 data = new ShareDataVersion1();
           increaseData(data, "increase-thread").start();
           increaseData(data, "increase-thread2").start();

           decreaseData(data, "decrease-thread").start();
           decreaseData(data, "decrease-thread2").start();
       }

       private Thread increaseData(ShareDataVersion1 data, String threadName) {
           return new Thread(() -> { for (int i = 0; i < 500; i++) data.increase(); }, threadName);
       }

       private Thread decreaseData(ShareDataVersion1 data, String threadName) {
           return new Thread(() -> { for (int i = 0; i < 500; i++)  data.decrease(); }, threadName);
       }
   }

   class ShareDataVersion1 {
       private int number = 0;
       private static final Logger LOG = LoggerFactory.getLogger(ShareDataVersion1.class);

       public synchronized void increase() {
           try {
               // 2.1 judge
               while (number != 0) { //      if (number != 0) {
                   this.wait();
               }
               // 2.2 work
               ++number;
               LOG.info(
                   Thread.currentThread().getName() + " increase shareData finished, number: " + number);
               // 2.3 notify
               this.notifyAll();
           } catch (Exception e) {
               LOG.error("increase failed");
           }
       }

       public synchronized void decrease() {
           try {
               // 2.1 judge
               while (number != 1) { //      if (number != 1) {
                   this.wait();
               }
               // 2.2 work
               --number;
               LOG.info(
                   Thread.currentThread().getName() + " decrease shareData finished, number: " + number);
               // 2.3 notify
               this.notifyAll();
           } catch (Exception e) {
               LOG.error("increase failed");
           }
       }
   }

   class ShareDataVersion2 {
       private int number = 0;
       private Lock lock = new ReentrantLock();
       private Condition condition = lock.newCondition();
       private static final Logger LOG = LoggerFactory.getLogger(ShareDataVersion1.class);

       public void increase() {
           lock.lock();
           try {
               while (number != 0) {
                   condition.wait();
               }
               ++number;
               LOG.info(
                   Thread.currentThread().getName() + " increase shareData finished, number: " + number);
               this.notifyAll();
           } catch (Exception e) {
               LOG.error("increase failed");
           } finally {
               lock.unlock();
           }
       }

       public void decrease() {
           lock.lock();
           try {
               while (number != 1) {
                   this.wait();
               }
               --number;
               LOG.info(
                   Thread.currentThread().getName() + " decrease shareData finished, number: " + number);
               // 2.3 notify
               condition.signalAll();
           } catch (Exception e) {
               LOG.error("increase failed");
           } finally {
               lock.unlock();
           }
       }
   }
   ```

## 8 lock

## synchronization
